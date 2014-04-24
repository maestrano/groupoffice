INSERT INTO `groupoffice`.`go_acl` (`acl_id`, `user_id`, `group_id`, `level`) VALUES ('999', '0', '1', '50'), ('999', '1', '0', '50'), ('999', '0', '3', '30');
INSERT INTO `ab_addressbooks` (`id`, `user_id`, `name`, `acl_id`, `default_salutation`, `files_folder_id`, `users`) VALUES
(999, 0, 'Contacts', 999, 'Default salutation', 2, 0);