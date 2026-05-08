# CASE FILE: Woosoo App MVP Ordering Backend

## Objective
Implement the backend ordering core for `woosoo-app` using Laravel 13.

## Repository Boundary
This case is scoped to `woosoo-app` only.

Do not modify:

- `woosoo-grillpad`
- `woosoo-nexus`
- `woosoo-print-bridge`

## Architecture Map

```mermaid
flowchart TD
    Tablet[Tablet PWA]
    Api[Laravel API /api/v1/device]
    Middleware[Device Token Middleware]
    Actions[Application Actions]
    Repos[Eloquent Repositories]
    DB[(Local App DB)]
    POS[Krypton POS Gateway]
    Reverb[Reverb Broadcast Events]

    Tablet --> Api
    Api --> Middleware
    Middleware --> Actions
    Actions --> Repos
    Repos --> DB
    Actions --> POS
    Actions --> Reverb
```

## State Machine

```mermaid
stateDiagram-v2
    [*] --> SessionStarted
    SessionStarted --> OrderPending: initial order submitted
    OrderPending --> OrderActive: POS accepted
    OrderPending --> OrderFailed: POS failed
    OrderActive --> RefillPending: refill submitted
    RefillPending --> RefillSubmitted: POS accepted
    RefillPending --> RefillFailed: POS failed
    OrderActive --> OrderClosed: admin/table reset
```

## Audit Checklist

- [x] Race conditions / async leaks considered
- [x] State machine / contract integrity considered
- [x] Security / auth boundaries considered
- [x] Monorepo / shared config drift considered
- [x] Test sufficiency seeded

## Security Notes

- Device tokens are returned only once during session start.
- Device tokens are stored as SHA-256 hashes.
- Broadcast payloads must not expose device tokens.
- POS gateway failures must not leak raw stored procedure details to clients.

## Race Condition Notes

- Active order creation checks for existing pending or active initial orders.
- Device row is locked during initial order creation when supported by the database driver.
- Reverb events are dispatched only after local DB commit.
