<a href="https://travis-ci.org/panayotovyura/bookmarks"><img src="https://travis-ci.org/panayotovyura/bookmarks.svg?branch=master"></a>

# Bookmarks

### For admin:

#### Run project:

1. composer install

2. php bin/console doctrine:database:create

3. php bin/console doctrine:schema:create

4. php bin/console hautelook_alice:doctrine:fixtures:load

5. php bin/console server:run (local php server on http://127.0.0.1:8000)

6. configuration for apache or nginx see http://symfony.com/doc/current/cookbook/configuration/web_server_configuration.html

#### PhpUnit:

1. php bin/console doctrine:database:create --env=test

2. php bin/console doctrine:schema:create --env=test

3. php bin/console hautelook_alice:doctrine:fixtures:load --env=test

4. ./vendor/bin/phpunit

#### Analysis Tools:

1. PHP_CodeSniffer: ./vendor/bin/phpcs --standard=psr2 src

2. PHP Mess Detector: ./vendor/bin/phpmd src text cleancode,codesize,controversial,design,unusedcode

3. PHP Copy/Paste Detector: ./vendor/bin/phpcpd src

### For frontend developer:

#### Get 10 latest bookmarks:

Example:
```sh
GET /bookmark
```
Response:
```sh
[
    {
        "uid": 1,
        "createdAt": "2016-06-16T15:37:31+03:00",
        "url": "http://google.com",
        "comments": []
    },
    {
        "uid": 2,
        "createdAt": "2016-06-15T23:39:26+03:00",
        "url": "http://www.howe.com/culpa-rem-aut-rerum-exercitationem-est-rem",
        "comments": [
            {
                "uid": 1,
                "createdAt": "2016-06-14T07:13:44+03:00",
                "ip": "218.230.103.77",
                "text": "Similique ad sed architecto quod nulla. Voluptas quibusdam inventore esse harum accusantium rerum nulla.",
                "bookmark": 2,
                "changeableAndDeletable": false
            },
        ...
	},
	...
]
```

#### Get bookmark by url:

Format:
```sh
GET /bookmark/{url}
```
Example:
```sh
GET /bookmark/http://google.com
```
Response:
```sh
{
    "uid": 1,
    "createdAt": "2016-06-16T14:43:20+03:00",
    "url": "http://google.com",
    "comments": [
        {
            "uid": 1,
            "createdAt": "2016-06-14T07:13:44+03:00",
            "ip": "218.230.103.77",
            "text": "Similique ad sed architecto quod nulla. Voluptas quibusdam inventore esse harum accusantium rerum nulla.",
            "bookmark": 1,
            "changeableAndDeletable": false
        },
        ...
    ]
}
```

#### Create bookmark:

Example:
```sh
POST /bookmark

{"url": "http://google.com"}
```
Response:
```sh
{
    "uid": 1
}
```

#### Create comment for bookmark:

Format:
```sh
POST /bookmark/{uid}/comment

{"text": "comment text"}
```
Example:
```sh
POST /bookmark/1/comment

{"text": "comment text"}
```
Response:
```sh
{
    "uid": 1
}
```

#### Update comment:

Format:
```sh
PUT /comment/{uid}

{"text": "comment text"}
```
Example:
```sh
POST /comment/1

{"text": "comment text"}
```
Response:
```sh
{
    "uid": 1
}
```

### Delete comment:

Format:
```sh
DELETE /comment/{uid}
```
Example:
```sh
DELETE /comment/1
```