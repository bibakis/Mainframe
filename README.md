## Mainframe
The complete PHP framework

MainframePHP is a framework based on (and extending) CodeIgniter.
Mainframe is to CodeIgniter what Ubuntu is to Debian.

---

### Why should I choose Mainframe over CodeIgniter ?

#### 1. Themes 

Use themes to keep your views tidy. No more loading headers/footers within views or CSS/JavaScript.

#### 2. Assets pipeline

Assets pipeline auto-magicaly renames your assets, compresses/minifies them and prevents browsers from serving older versions when you make changes. It also auto compiles less files to CSS.

Loading assets is easy too:

```
css('themes/my_theme/css/styles.css');
js('themes/my_theme/less/my_script.js');
less('themes/my_theme/less/styles.less');
```

#### 3. MySQL syncronization

If you are member of a team you can now share all changes to your DB schema & data over your favourite VCS like git. 

Mainframe will generate XML files with any changes you do to your database and the rest of the team will be auto-updated when they do the next pull from git.

#### 4. Unit testing reporting

If you use CodeIgniter's build in unit testing you can have Mainframe automatically generate unit test reports to see any issues at a glance.

#### 5. Use mini-mvc-apps with plugins

Do you have an app with a million controllers which feels a mess ?

Organize your app in many mini-MVC apps using plugins. You just place your files in /app/plugins/ using the same MVC structure as your main app.

#### 6. Multiple apps in subdomains

Do you want to serve a dedicated mobile version of your app ?

Simply create a new app_mobile and have it served from mobile.mydomain.tld

#### 7. The best of open source

If you are creating dynamic & responsive apps (like you should) you'll feel right at home. Mainframe includes the latest Foundation CSS framework, the latest jQuery and other popular open source libraries.

---

### Ok, I'm sold, where are the docs ?

They are work in progress. Check the /docs/mainframe folder for the latest documentation.