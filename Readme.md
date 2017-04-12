Mautic for Prestashop 1.6 and 1.7
=====================
Popular plugin now as open source. 
Contributions are welcome as Pull requests.

Video
-----

https://www.youtube.com/watch?v=2aCzgPeKtcs 

Screenshots
-----------

http://1drv.ms/20N1JqH

Documentation (online)
----------------------

https://1drv.ms/w/s!AvXHYm0qUE5irus4BBiovLPZjbTEqw

Features
--------

Add Mautic Tracking pixel to website
Lead Identification by Cookie, Customer ID, Guest ID
Lists Mapping
Add to the list If customer is added to newsletter
Add to the list If customer want receive special offers from partners!
Add to the list If Cart is created
Add to the list If Order is created
Remove from the list If Order is created
Fields Mapping (company, lastname, firstname, address1, address2, postcode, city, phone, phone_mobile, vat_number, dni, id_gender, birthday, email, newsletter, optin, website)


FAQ
---

**How Can I Use Mautic?**

www.mautic.org (free download for experienced users)
https://mautic.com (hosting version for your Mautic)


**How Can I add Mautic Tracking pixel to website?**

See module options, add base URL of the Mautic instance and enable it.

**Where Can I Find Client key and Client secret key?**

See your Mautic > Settings > API Credentials. 
We currently only support OAuth1 protocol.

**How Does Lead Identification work?**

From **mtc_id** cookie parameter.

**How Does Lists Mapping work?**

Just create your list in Mautic. We add/remove users/leads when actions are triggered.

We support triggers: add to newsletter and special offers, create cart and finish order.

**How Does Fields Mapping work?**

We can send information about customer to your Mautic. Currently We support these informations: company, lastname, firstname, address1, address2, postcode, city, phone, phone_mobile, vat_number, dni, id_gender, birthday, email, newsletter, optin, website…

**Troubleshooting**

The tracing doesn't work for logged in Mautic administrators so the statistics aren't deceived by Mautic administrators looking at the page result while editing a page. So make sure you are logged out of Mautic or use an incognito browser window while testing the tracking.

What  is Mautic?
------------------------

[Mautic](https://www.mautic.org/) is open source multi channel marketing automation software. Mautic provides detailed lead tracking along with powerful lead nurturing tools to help you organize your marketing campaigns. Mautic automates the process of finding and nurturing leads through landing pages and forms, sending email, tracking social media, and integrating with your CRM and other systems.
