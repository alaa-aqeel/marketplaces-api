# This project is a sample of the **Marketplace Aggregator** API with **Laravel**.
It unifies product data from multiple marketplaces like (AliExpress, Alibaba, Shein, 1688) into a single API with consistent structure.


## 🚀 Features

- **Multi-Source Product Integration**
  - [X] Fetch products from marketplaces (different APIs). 
  - [X] Normalize product data into a unified schema.

- **Search Capabilities**
  - [X] Keyword search with *Full-Text Search* (DB).
  - [X] Search by product link (extract marketplace + product ID).
  - [ ] Image search.

- **Performance & Reliability**
    - [X] Caching layer (Redis/File).
    - [X] Circuit Breaker for external APIs.
    - [X] Monitoring (p50, p95, p99 latency). 


## 🧪 Testing
This project uses DummyJSON instead of real marketplace APIs for testing:
- https://dummyjson.com/products/search → product list 
- https://dummyjson.com/products/1 → single product
Adapters can later be replaced with real marketplace integrations.

## 🛠️ Tech Stacklaravel telescope
- **Backend:** Laravel 
- **Database:** PostgreSQL 
- **Cache:** Redis 
- **Containerization:** Docker [Laravel Sail]


## ⚙️ Installation

### 1. Clone the repository
```bash
$ git clone https://github.com/your-username/marketplace-aggregator.git
$ cd marketplace-aggregator
$ cp .env.example .env
$ ./vendor/bin/sail up -d
$ ./vendor/bin/sail artisan migrate
$ ./vendor/bin/sail artisan db:seed --class=ProductSeeder # dummy products
$ ./vendor/bin/sail artisan latency:percentiles # show  latency of endpoints
```
