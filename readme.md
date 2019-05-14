### Spec
Site Visitor can:
- see list of books
- delete a book from the list
- change an authors name
- sort by title or author
- search for a book by title or author
- export the the following in CSV and XML
    - A list with Title and Author
    - A list with only Titles
    - A list with only Authors

### Separate frontend and backend
I like the idea about dividing backend and frontend from each other. 
And I use this approach when possible. 
In this project my Backend represents RestFul Api and the Frontend as a client of this Api.
The Frontend is made with using Vue.js

### Coding style
The backend uses "*PSR-2*".
I like to keep my code in PSR coding standards. 
So I've added "code sniffer" (squizlabs/php_codesniffer). 
This can help to keep the code in compliance with the standards in IDE. 
Also it will check the code in CI

### Steps to install
1. `composer install`
2. `@php -r "file_exists('.env') || copy('.env.example', '.env');"`
3. `php artisan key:generate`
4. Tweak `.env` file
5. `php artisan migrate`
6. `npm install`
7. `npm run dev`

### Fake data
To fill in the database with test data run `php artisan db:seed` 

### Code explanation
As the backend is Restful Api and I assumed that different clients can access it
(not only out of the box), I've decided to make some version separation.
You can see it in `tests` and `controllers`

I like using standard Laravel resources controllers. 
But for Api I change name of the `index` method to `get`, 
and `show` method to `getSpecific`  