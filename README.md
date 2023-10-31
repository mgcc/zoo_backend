### Zoo application


## Backend CRUD API for for Animal records

The structure of Animal records is as follows:

```
{
  id: number
  name: string,
  type: string (),
  conservationStatus: string,
  created_at: timestamp,
  updated_at: timestamp,
}
```

List of endpoints

 * GET /api/animal?
   GET /api/animal?name=string&type=string&conservationStatus=string
     - returns list of all animals
     - optional filters for name, type, and conservationStatus
 * GET /api/animal/{id}
     - returns a single record of an animal with the specified id
 * POST /api/animal
     - saves a record of a single animal
     - expects a JSON payload of the following structure: { name: string, type: string, conservationStatus: string }
 * PUT /api/animal/{id}
     - updates a record of an existing animal of the specified id
     - expects a JSON payload of the following structure: { name: string, type: string, conservationStatus: string }
 * DELETE /api/animal/{id}
     - deletes a record of an animal of the specified id


## To run the application:

Backend
 - composer install
 - php artisan migrate:fresh
 - php artisan serve

Frontend
 - npm install
 - npm start

Access the frontend at http://localhost:3000