const express = require('express')
const app = express()
const port = 2990;
const cors = require('cors')
app.use( cors() )        
 
const mjml2html = require('mjml');

app.use(express.json()) 
    
app.post('/test', (req, res) => {

    res.send('you sent: ' + JSON.stringify(req.body))
});

app.post('/mjml', function (req, res) {

    //validate
    if(!('mjml' in req.body)) {
         
        res.send('Didn\'t find the appropriate property')

    }else{
      
        //convert mjml and return html
        let htmlString = mjmlToHtml(req.body.mjml);
        if(htmlString.constructor === Object && Object.keys(htmlString).length > 0 && 'errors' in htmlString ) {
            res.status(500)
            res.json({ error: 'Mjml conversion failed. ' + JSON.stringify(htmlString) })
        }else{
            
            res.send(htmlString)
        }
    }
  
});

app.listen(port, () => console.log(`Example app listening on port ${port}!`))
 

/* =============== Convert MJML to HTML ================================ */

// function expects a string with mjml code (as tags)
function mjmlToHtml( mjmlReq ) {
   
    const htmlOutput = mjml2html(mjmlReq);
   
    return htmlOutput.errors.length ? {errors: htmlOutput.errors} : htmlOutput.html;

}