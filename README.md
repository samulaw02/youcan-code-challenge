## Implementation Overview

### What I have done

I have implemented what was requested for which includes but not limited to:

* **CRUD Operation On Products And Category On Web, API AND CLI:** User can create, read, update and delete products and category on Web, API and CLI.

* **Advanced Search Functionality:** User can search for products through name, description and category name.

* **Sorting:** User can sort product based on price.

* **Automated Unit Test:** I also implemented automatic unit testing via github actions and PHPUnit

* **CI/CD:** I dockerized the application and also setup github actions

* **RedisCache Layer :** I used redis to maintain a persistent cache strategy

* **Elastic Search :** I used elastic search for the search functionality and a fallback strategy to eloquent whenever there is an issue with elastic search.

* **Laravel Jobs :** I used Laravel Job for async operations in making sure that elastic search is in sync with data source.

* **Custom Logger :** I implemented a custom logger that log inline in dev and json in production.

* **Seeder :** I also implemented database seeders.

* **Artisan Commands :** I implemented various artisan commands which are listed below:


### Artisan Commands

* create a category:                `php artisan category:create`
* delete a category:                `php artisan category:delete {id}`
* list categories:                  `php artisan category:list {page?}`
* read category:                    `php artisan category:read {id}`
* update category:                  `php artisan category:update {id}`
* create a product:                 `php artisan product:create {category_ids*}`
* delete a product                  `php artisan product:delete {id}`
* index existing products to es     `php artisan index:products`
* list products                     `php artisan product:list {page?}`
* read a product                    `php artisan product:read {id}`
* update a product                  `php artisan product:update {id} {--category_ids=*}`
* seed database                     `php artisan seed:database`


### Things to remember
* **Add these enviroment variable:** setting up a correct .env require the following.
1.  ELASTICSEARCH_HOST=
1.  ELASTICSEARCH_PORT=
1.  ELASTICSEARCH_SCHEME=
1.  ELASTICSEARCH_USER=
1.  REDIS_CLIENT=predis
1.  LOG_CHANNEL=custom


### Conclusion
I thoroughly enjoyed working on this challenge and implementing the requested features. If you have any questions or need further information, please feel free to reach out. Thank you for this opportunity!
