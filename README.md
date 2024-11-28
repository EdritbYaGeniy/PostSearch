Создание базы данных:

``` SQL
CREATE DATABASE UsersPosts;
```

Создание столбца users:

``` SQL
CREATE TABLE users (
    id SERIAL PRIMARY KEY,       
    name VARCHAR(100) NOT NULL,  
    username VARCHAR(50) NOT NULL, 
    email VARCHAR(100) NOT NULL  
);
```

Создание столбца posts:

``` SQL
CREATE TABLE posts (
    id SERIAL PRIMARY KEY,       
    userId INT NOT NULL,         
    title TEXT NOT NULL,         
    body TEXT NOT NULL,          
    FOREIGN KEY (userId) REFERENCES users (id) 
);
```