-- Create user_favorites
CREATE TABLE `kpower`.`user_favorites` (
  `user_id` INT UNSIGNED NOT NULL,
  `page_id` INT UNSIGNED NOT NULL,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`, `page_id`),
  INDEX `fk_favorite_page_idx` (`page_id` ASC),
  CONSTRAINT `fk_favorite_page`
    FOREIGN KEY (`page_id`)
    REFERENCES `kpower`.`pages` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `fk_favorite_user`
    FOREIGN KEY (`user_id`)
    REFERENCES `kpower`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION);

-- get user favorites
SELECT pages.id, title, category
FROM categories JOIN pages
        ON categories.id = pages.id
JOIN user_favorites
        ON user_favorites.page_id = pages.id
WHERE user_id = 1 AND page_id = 1
ORDER BY title;