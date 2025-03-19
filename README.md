## Installation

### Prerequisites
- PHP (>= 8.2)
- Composer
- Symfony CLI
- Redis (for caching)
- A database (MySQL)

### Installation Steps

1. **Clone the Repository**
   ```bash
   git clone https://github.com/your-repo/your-project.git
   cd your-project



2. **Install Dependencies**
   ```bash
   composer install
   ```


3. **Load User Data Fixtures**
   ```bash
   php bin/console doctrine:fixtures:load
   ```


### Generate Dummy Data Command

This Symfony command generates dummy property data in JSON format and clears the cached property data. It is useful for quickly creating sample data during development or testing.

## Overview

The command creates a specified number of property records with the following fields:
- **id**: A sequential identifier.
- **address**: A fake street address generated using Faker.
- **price**: A random property price between 100,000 and 500,000.
- **source**: A string indicating the data source.


Run the command using Symfony's console tool. Below are two examples:

### Example 1: Generate Data for `source1.json`

Generate 100 dummy records using "Source1" as the data source and save the output to `source1.json`:

```bash
php bin/console app:generate-dummy-data --records=100 --source=Source1 --output=source1.json
```

### Example 2: Generate Data for `source2.json`

Generate 100 dummy records using "Source2" as the data source and save the output to `source2.json`:

```bash
php bin/console app:generate-dummy-data --records=100 --source=Source2 --output=source2.json
```

## Clearing Redis Cache

Using Redis CLI  
To manually clear the Redis cache, run:

```bash
redis-cli flushall
```

## Testing API & Authentication with JWT in Postman

### Generate JWT Authentication Keys

If JWT authentication is not yet set up, generate the private and public keys:

```bash
php bin/console lexik:jwt:generate-keypair
```
This command creates keys inside the config/jwt/ directory.


### Step 1: Authenticate & Get JWT Token

#### Open Postman.
#### Select POST as the request type.
#### Enter the URL:

```bash
https://127.0.0.1:8000/api/login
```
#### Go to the Body tab and select raw.
#### Set the Content-Type to application/json.
#### Enter the following JSON data (Ensure that data fixtures are loaded first):

```Json
{
    "email": "testing@gmail.com",
    "password": "testing"
}

```


#### Click Send.
Response (If login is successful):
```Json
{
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE3NDIzODMyOTksImV4cCI6MTc0MjM4Njg5OSwicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoidGVzdGluZ0BnbWFpbC5jb20ifQ.WvuA7D3eYzp7V3SAtcU8spdCG-a9JKjiRCZ03DZaY0zrt3vSHJdep0P-Uq2dJIxxGAqlQ1oL2J59H70tWiAqEY2ENlcSBKC__MgOv72ieRKbnY3sZO7zvhufQH1BkKEl3roLl87QyAPUkUMnUm100BJoU1SlqHUkvX4y_1NaJUIyP1fnHid6GfrLGtHWdVW4DwYx3a60mUyTEay_8MdeIWvRqmTlvz7x2RX_dx0mFkgNOL6abA2qtMeBucbOaRS7JWw_9jWBByQxUal02RJ2dOtcmrMEBpj_Fd1uYu-SsLYj7tq_pwg_KBX-eiQ57JM4SR46zF8JTHe4bLGRPkiVFA"
}
```

### Step 2: Access the Protected API Endpoint

#### Open Postman.
#### Select POST as the request type.
#### Enter the URL:

```bash
https://127.0.0.1:8000/properties
```

#### Go to the Headers tab.
#### Add a new header

##### Key: Authorization
##### Value: Bearer your-jwt-token-here

#### Click Send.

```Json
{
    "user": "testing@gmail.com",
    "roles": [
        "ROLE_USER"
    ],
    "properties": [
        {
            "id": 1,
            "address": "177 Beverly Plaza",
            "price": 371853,
            "source": "Source1"
        },
    ]
}        
```