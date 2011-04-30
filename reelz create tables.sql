CREATE TABLE reelzusers(
user_id INT(9) NOT NULL AUTO_INCREMENT,
first_name VARCHAR(20),
last_name VARCHAR(20),
email VARCHAR(60) NOT NULL,
password VARCHAR(40) NOT NULL,
def_location VARCHAR(80),
fav_theater VARCHAR(60),
fav_genre VARCHAR(20),
fav_movie INT(9),
fav_food ENUM('bagels', 'bakeries', 'beer_and_wine', 'breweries', 'coffee', 'convenience', 'desserts', 'diyfood', 'donuts', 'farmersmarket', 'fooddeliveryservices', 'grocery', 'icecream', 'internetcafe', 'juicebars', 'gourmet', 'candy', 'cheese', 'chocolate', 'ethnicmarkets', 'markets', 'healthmarkets', 'meats', 'seafoodmarkets', 'tea', 'wineries'),
active SMALLINT(1) NOT NULL DEFAULT 1,
date_inactivated DATETIME,
PRIMARY KEY(user_id),
FOREIGN KEY(fav_movie) REFERENCES reelzmovies(movie_id)
);

CREATE TABLE reelzmovies(
movie_id INT(9) NOT NULL,
title VARCHAR(40) NOT NULL,
year INT(4) NOT NULL,
html_header TEXT NOT NULL,
html_desc TEXT NOT NULL,
reviews TEXT,
views INT(5) DEFAULT 1 NOT NULL
);
