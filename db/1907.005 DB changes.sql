CREATE TABLE `tourdb2_prod`.`tbl_users` 
    ( 
        `usrId` INT(11) NOT NULL AUTO_INCREMENT COMMENT 'Unique Id of table' , 
        `usrLogin` VARCHAR(50) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT 'Login the user enters to access the db' , 
        `usrFirstName` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
        `usrLastName` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
        `usrEmail` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL , 
        `usrPasswd` BLOB NOT NULL COMMENT 'password encrypted (aes)', 
        PRIMARY KEY (`usrId`), 
        INDEX `usrLogin` (`usrLogin`)
    ) ENGINE = InnoDB;

