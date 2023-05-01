# k-ma-urlshortener
Very simple URL shortener used for k-ma.eu. Path patterns / URLs can be configured via JSON file. 

## Roadmap
- [x] redirect simple strings
- [x] have default URL
- [ ] use regexes
- [ ] stats
- [ ] nice error page

## Installation
If you want to use that URL shortener for own projects, installation would be really simple. No database etc. is needed, just a simple webserver, rewrite engine and PHP: 

1. Install Webserver
`sudo apt-get install apache2 php git `

2. Clone repository
`cd /var/www`
`git clone git@github.com:Kolping-Mannheim/k-ma-urlshortener.git`

3. Configure virtual host, ensure that AllowOverride is enabled:
```
<VirtualHost *:80>
        ServerName your-domain.eu
        ServerAlias www.your-domain.eu

        ServerAdmin your@email.de
        DocumentRoot /var/www/k-ma-urlshortener

        <Directory /var/www/k-ma-urlshortener/>
                AllowOverride All
        </Directory>

        # Logs, other config, redirect for SSL, ...
</VirtualHost>
```

4. Enable rewrite module in Apache
`a2enmod rewirte`
`service apache2 restart`

5. Update config.json to fit your requirements
One redirection block looks like this:
```
{
    // path that should redirect
    "path": "abc",

    // final URL
    "url": "https://www.example.com/abc/wuff/long-URL",

    // if permanent is set to true, HTTP 301 instead of 302 is used; clients can cache redirection
    "permanent": true
}
```

6. Optional: Enable SSL via Let's Encrypt: 
`sudo apt-get install certbot python3-certbot-apache`
`certbot --apache`

