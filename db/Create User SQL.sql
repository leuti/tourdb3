/* SQL to create new user incl. password encryption 
   The AES encryption is used (see: https://zinoui.com/blog/storing-passwords-securely)
*/

INSERT INTO `tbl_users` (
    `usrLogin`, 
    `usrFirstName`, 
    `usrLastName`, 
    `usrEmail`, 
    `usrPasswd`) 
    VALUES (
        'leut',
        'Danny', 
        'Leutwyler',
        'daniel.leutwyler@gmx.ch',
        AES_ENCRYPT('sugus', 'vjLzGfqxnOFEWCpIbeXdFjnPWTKcjo9a')
        );


/* SQL to select  user incl. password decryption 
   The AES decryption is used (see: https://zinoui.com/blog/storing-passwords-securely)
*/
SELECT AES_DECRYPT(`usrPasswd`, 'vjLzGfqxnOFEWCpIbeXdFjnPWTKcjo9a') AS `usrPasswd` 
FROM `tbl_users` WHERE `usrLogin` = 'leut';