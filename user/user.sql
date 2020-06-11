CREATE TABLE users
(
id bigint NOT NULL AUTO_INCREMENT COMMENT 'id',
user_name varchar(50) DEFAULT '' NOT NULL COMMENT 'ユーザー名',
password varchar(100) DEFAULT '' NOT NULL COMMENT 'パスワード',
created datetime NOT NULL COMMENT '作成日時',
modified datetime NOT NULL COMMENT '更新日時',
deleted datetime COMMENT '削除日時',
PRIMARY KEY (id)
)
