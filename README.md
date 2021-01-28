# MJML Laravel example

## Description
This is an example to use a newsletter template in the form of a blade template with mjml inside.
In this example there are certain models and relations being used, they are not being explained here but only used as an example. The model relations and data table columns will be different in your case.

## Dependencies

### Composer (PHP) dependencies
 
* Laravel

### NPM dependencies

* mjml
* express
* cors
  
## Procedure


### **Save (update(create) newsletter data)**

You will typically have an admin panel or CMS where you can create/edit newsletters ( these things are not included here).


#### **Method: Controllers\NewslettersController: store**

In this controller method the data is retrieved and saved, then the newsletter is dispatched to all newsletter subscribers.

#### **Method: Newsletter :saveNewsletter**

Retrieves the data, calls ``getDataForSave()``  which organize the data into an associative array.
Saves it.

### **Send newsletter (involves converting blade template to mjml and to html**
Newsletter subscribers are mailed with the 
#### **Method: Newsletter::dispatchNewsletterToMailinglists**

Retrieves the template data, then loops the mailinglists, and calls ``sendNewsletterToList()`` for each list.

#### **Method: Newsletter: sendNewsletterToList**

The mail is constructued using "Mailable" class **Newsletter** (aliased "NewsletterTemplate") is instantiated with the template data. See **Mailable: Newsletter** below.

For each list subscriber, the mail job **SendEmail** is run, which sends the mail.

#### **Mailable: Newsletter**

Mjml render method ``renderMjml()`` is called.

#### **CustomClasses\MjmlHandler: renderHtml **

Renders a blade view - see ```views\emails\templates\template1.blade.php```
The resulting mjml code is included in a call to ``postToNodeExpressConvertMjml()`` 

#### **CustomClasses\MjmlHandler: postToNodeExpressConvertMjml **

The mjml-code is being sent to a local node server endpoint to be converted to html.

#### **mjml/index.js**

Here the data is run in the mjml method **mjml2html** and the resulting html is returned back.

(Make sure that the express server is running).