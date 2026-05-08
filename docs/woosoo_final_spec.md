# Woosoo MVP Ordering Backend Specification (Working Reference)

This repository implements a lightweight Laravel 13 backend slice for device ordering.

## MVP backend scope

- Device session start/restore
- Initial order creation
- Refill order submission
- Active order lookup
- Print event listing and acknowledgement
- POS gateway isolation via contracts
- Local persistence via repositories
- After-commit domain event broadcasting for order/print lifecycle updates

## Core model boundaries

- `Device`
- `DeviceOrder`
- `DeviceOrderItem`
- `PrintEvent`

## Contracts

- `PosOrderGateway`
- `PosContextGateway`
- `DeviceOrderRepository`
- `PrintEventRepository`

## Notes

- POS integration must stay behind gateway contracts.
- API tests should use fake or mocked gateways and must not require a live POS DB.
- Broadcast payloads must not include bearer token material.
