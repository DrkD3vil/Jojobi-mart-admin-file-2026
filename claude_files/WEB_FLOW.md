# JOJOBI Backoffice — Web Flow

Diagrams of how a request actually moves through `admin/`, from the shared
access-control gate down to the major business flows. Renders as Mermaid in GitHub,
VS Code (Markdown Preview), and most modern Markdown viewers.

---

## 1. Request / Access-Control Pipeline

Every route except the signed public order-status link passes through this exact
sequence before a controller method runs.

```mermaid
flowchart LR
    A[Request] --> B{auth session?}
    B -- no --> B1[redirect: tyro-login.login]
    B -- yes --> C[tyro.access middleware]
    C --> D{admin / super-admin role?}
    D -- yes --> H[Controller action]
    D -- no --> E{route has access_key?}
    E -- no --> H
    E -- yes --> F[AccessService.canAccessKey]
    F --> G{PrivilegeAccessKey match<br/>by user, role, or global?}
    G -- yes --> H
    G -- no --> I[Log::warning + abort 403]
```

- Matches are cached 1h per `access_key` (`Cache::remember`).
- **Deny by default**: no matching row = 403 (`allowIfNoMapping = false`).
- `AiAssistantController@index/state/requestAccess` deliberately skip step E so an
  unprivileged user sees a "request access" screen instead of a bare 403.

```mermaid
flowchart LR
    QR[Receipt QR code] --> U["/order-status/{order}?signature=..."]
    U --> S{Laravel signed<br/>URL check}
    S -- valid --> P[PublicOrderController.show<br/>no login required]
    S -- tampered --> X[403]
```

---

## 2. POS Checkout Flow

```mermaid
flowchart TD
    A[Cashier opens /cart] --> B[CartController.search /<br/>quickProductSearch]
    B --> C[CartController.add]
    C --> D[lockActiveCart + assertBatchStockAvailable<br/>row-locks BatchStock]
    D --> E[CartGiftService.sync]
    E --> F{Qualifies for<br/>free gift?}
    F -- yes --> G[Attach gift CartItem<br/>clamped to location stock]
    F -- no --> H[removeInvalidAutoGifts]
    G --> I[CartController.setCustomer<br/>optional]
    H --> I
    I --> J[CartController.applyRewards<br/>optional]
    J --> K[CartController.checkout]
    K --> L[OrderController.storeFromCart]
    L --> M[Cart -> Order conversion<br/>same stock-lock discipline]
    M --> N[PaymentController.store]
    N --> O[InvoiceController.print<br/>dompdf, logo inlined]
```

---

## 3. Order Lifecycle

```mermaid
stateDiagram-v2
    [*] --> pending
    pending --> processing: OrderController.process
    processing --> completed: OrderController.complete
    completed --> paid: PaymentController.store
    paid --> refunded: OrderController.refund
    paid --> returned: ReturnController.store
    pending --> cancelled: OrderController.cancel
    processing --> cancelled: OrderController.cancel
    completed --> [*]
    refunded --> [*]
    returned --> [*]
    cancelled --> [*]

    paid --> paid: OrderSplitController.split\n(parent + child order)
```

Soft-deleted orders go through a separate trash lane: `trash` → `restore` /
`restoreMultiple` → `forceDelete` / `forceDeleteMultiple` / `emptyTrash`.

---

## 4. Order Split / Merge

```mermaid
flowchart LR
    A[Paid Order] --> B[OrderSplitController.preview]
    B --> C[OrderSplitController.split]
    C --> D[createChildOrder]
    D --> E[moveItemsToChildOrder]
    E --> F[prorateDiscount<br/>parent vs child]
    F --> G[createSplitRecord<br/>OrderSplit row]
    G --> H[handleSplitPayments<br/>reconcile payments across both orders]
    H --> I((Parent Order)) & J((Child Order))

    J -.merge back.-> K[OrderSplitController.merge]
    I -.merge back.-> K
    K --> L[moveItemToParent]
    L --> M[mergeChildPayments]
    M --> I
```

---

## 5. Returns & Exchange Flow

```mermaid
flowchart TD
    A[ReturnWizardController.index] --> B[searchOrder / searchCustomer]
    B --> C[selectOrder]
    C --> D[ajaxOrderItems]
    D --> E[ReturnController.store]
    E --> F[InventoryService.post<br/>stock returns to BatchStock]
    F --> G[StockLedger entry written]
    G --> H[recalcOrderTotals]

    D2[ExchangeController.create] --> D3[ajaxOrders / ajaxOrderItems]
    D3 --> D4[ajaxBatches / ajaxAvailability<br/>pick replacement batch]
    D4 --> D5[ExchangeController.store]
    D5 --> F
    D5 --> I[exch_applyExchangeToOrder<br/>reprice order for the swap]
```

---

## 6. Stock Movement — the InventoryService Funnel

Every stock-affecting action in the app, regardless of trigger, converges on the
same locked write path.

```mermaid
flowchart TD
    T1[StockTransferController.store] --> S[InventoryService.post]
    T2[ReturnController.store] --> S
    T3[ExchangeController.store] --> S
    S --> L1[lockStockRow<br/>row-lock BatchStock]
    L1 --> L2[applyTransferLine]
    L2 --> L3[writeLedger<br/>immutable StockLedger row]
    L3 --> O[BatchStockObserver.updated]
    O --> M[RealtimeMetricsService<br/>low-stock counter]
    L3 --> V[StockLedgerController<br/>read-only audit trail]
```

---

## 7. AI Assistant Conversation Loop

```mermaid
sequenceDiagram
    participant U as User
    participant C as AiAssistantController
    participant S as GeminiAssistantService
    participant G as Gemini API
    participant D as dispatchToController

    U->>C: POST /ai-assistant/chat
    C->>S: sendMessage(user, message)
    S->>S: availableToolsFor(user)<br/>filtered by AccessService
    S->>G: callGemini(history + tool declarations)
    G-->>S: reply or tool_call

    alt read-only tool requested
        S->>S: run tool (e.g. getLowStockProducts)
        S->>G: callGemini(tool result)
        G-->>S: final reply
    else write tool requested<br/>(createProduct / createCategory / createProductBatch)
        S-->>U: confirmation prompt<br/>(stored as "pending" in session)
        U->>C: POST /ai-assistant/resolve
        C->>S: resolvePending(user, decision)
        alt approved
            S->>D: dispatchToController(controller, method, args, user)
            D-->>S: result
        else declined
            S-->>U: cancelled, no side effect
        end
    end

    S->>S: logTurn -> AiAssistantLog
    S-->>U: reply
```

Conversation history and any pending confirmation live in the **session**
(`historyKey`/`pendingKey` per user), not the database — only the final tool-call
outcome is persisted, to `AiAssistantLog`.

---

## 8. RBAC Access-Key Grant Flow

```mermaid
flowchart LR
    A[Admin opens /access-keys] --> B[AccessKeyMappingController.store]
    B --> C{Grant target}
    C -- user_id --> D[PrivilegeAccessKey row:<br/>user_id set, role_id null]
    C -- role_id --> E[PrivilegeAccessKey row:<br/>role_id set, user_id null]
    C -- everyone --> F[PrivilegeAccessKey row:<br/>both null = global grant]
    D & E & F --> G[AccessService.clearCacheForAccessKey]
    G --> H[Next request re-warms<br/>the 1h cache on read]
```

Also branching off `/access-keys`: `approveAiAccessRequest` /
`denyAiAccessRequest` resolve rows in `AiAccessRequest` created when a user without
the `ai_assistant` key clicks "request access" on the assistant's landing page.
