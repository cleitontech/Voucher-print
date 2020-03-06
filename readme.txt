CAPTIVE PORTAL VOUCHERS PRINT TEMPLATE
------------------------------------------------------------------------------
By Daniele Arrighi <daniele.arrighi@gmail.com>

Credits:
Reward Gagarin <rewardgms@gmail.com>
Ikkuranus
------------------------------------------------------------------------------
(Access via SCP Protocol using root as username)

1. Edit css file to fit your needs and overwrite logo.png with your onw logo
2. in /usr/local/www/ overwrite services_captiveportal_vouchers.php
3. in /usr/local/www/ upload services_captiveportal_vouchers_options.php and services_captiveportal_vouchers_print.php
4. create a new folder named: "voucherfiles" (without ", case sensitive)
5. upload voucherfiles directory's files in the newly created directory
6. Print from Services -> Captive Portal -> Vouchers

Extra:
If you want people with more limited accounts to print out vouchers without having full access to the WebUI add the portalprint.priv.inc file in the /etc/inc/priv/ folder. (Not tested in 2.4.4)
