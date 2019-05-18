### Spec
Site Visitor can:
- see list of books
- add a book to the list
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
The backend uses "**PSR-2**".
I like to keep my code in PSR coding standards. 
So I've added "code sniffer" (squizlabs/php_codesniffer) as a composer dependency. 
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

### Tests
run for tests `vendor\bin\phpunit`
Notice that tests uses same database, 
So you need remigrate database after running tests 
(usually I use another database for testing, but for small project like this for simplifying I use same DB)

### Code explanation
As the backend is Restful Api I assumed that different clients can access it
(not only out of the box), I've decided to make some version separation.
You can see it in `tests` and `controllers`

I like using standard Laravel resource controllers. 
But for Api I usually change name of the `index` method to `get`, 
and `show` method to `getSpecific`  

**Sorting** - Even though according to the spec there should be 
sorting by title or author, I added possibility to combine sorting by both fields
you can see how it works in tests. 

Also you can easily add any other field for sorting and combine them

If broken data is provided they will be ignored 
(as it's not user inputs I've decided not to throw exceptions under validation)

**Filtering** - 
As well as in sorting you can easily add any other field for filtering

Also you can easily add any comparison operator or another logic you want

Api examples:
* 1
```
    let params = {
        "filter_groups": [
            {
                "filters": [
                    [
                        "field" => "title",
                        "value" => "some string",
                        "operator" => "like"
                    ]
                ]
            }
        ]
    }
    
    // ?filter_groups[]={"filters":[{"field":"title","value":"some string","operator":"like"}]}
```
will query:
```
    SELECT * FROM `table_name` 
    WHERE `title` LIKE '%some string%'
```
...

* 2
```
    let params = {
        "filter_groups": [
            {
                "filters": [
                    [
                        "field" => "title",
                        "value" => "some string",
                        "operator" => "like"
                    ],
                    [
                        "or" => true
                        "field" => "author",
                        "value" => "some name",
                        "operator" => "like"
                    ]
                ]
            }
        ]
    }
    
    // ?filter_groups[]={"filters":[{"field":"title","value":"some string","operator":"like"},{"or":true,"field":"author","value":"some name","operator":"like"}]}
```
will query:
```
    SELECT * FROM `table_name` 
    WHERE `title` LIKE '%some string%' OR `author` LIKE '%some name%'
```
...

* 3
```
    let params = {
        "filter_groups": [
            {
                "filters": [
                    [
                        "field" => "title",
                        "value" => "Anna Karenina",
                        "operator" => "like"
                    ],
                    [
                        "or" => false
                        "field" => "author",
                        "value" => "Jon Snow",
                        "operator" => "like"
                    ]
                ]
            },
            {
                "or": true
                "filters": [
                    [
                        "field" => "title",
                        "value" => "Harry Potter",
                        "operator" => "like"
                    ]
                ]
            }
        ]
    }
    
    // ?filter_groups[]={"filters":[{"field":"title","value":"Anna Karenina","operator":"like"},{"or":true,"field":"author","value":"Jon Snow","operator":"like"}]}&filter_groups[]={"filters":[{"field":"title","value":"Harry Potter","operator":"like"}]}
```
will query:
```
    SELECT * FROM `table_name` 
    WHERE 
    (`title` LIKE '%Anna Karenina%' AND `author` LIKE '%Jon Snow%')
    OR
    (`title` LIKE '%Harry Potter%')
```

(You can add as many groups as you want, as well as filter blocks)

P.S. Of course you can combine sorting and filtering
