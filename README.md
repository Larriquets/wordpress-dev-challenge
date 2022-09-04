# WordPress Challenge 
# Dev : Matías Larriqueta



This Plugin implements the requirements of this **[link](https://mcontigo.notion.site/Instrucciones-prueba-WordPress-0ab955afeefa428c9b25b74c221f2f46)**.


## Challenge #1
- A Metabox Fields is created for the Post.
- The shortcode is created : [mc_citations post_id=""].
If the "post_id" parameter is empty, otherwise the content of the entered id will be displayed.

<img src="https://staketo.wpengine.com/wp-content/uploads/2022/09/FireShot-Capture-011.png"  width="600"/>





## Challenge #2

### How it works:
When the plugin is installed a check is made on each Post. If a conflicting link is found the information is saved in the Metabox of that Post.
Then the Cron performs the same action on Posts that were not tested (new) or corrected.

## Steps :

#### - Metabox Fields is created in each Post.

- Register Cron Events.
- Register Links errors in the post_content.
- Register Object with the detail of the errors.
- Metabox updated with Post testing results. 
 <img src="https://staketo.wpengine.com/wp-content/uploads/2022/09/FireShot-Capture-009.png"  width="600"/>

#### - The Admin Page "Monitor Links - Post Content" is created. 
This page shows a table with all the faulty links detected.
 
<img src="https://staketo.wpengine.com/wp-content/uploads/2022/09/FireShot-Capture-011-Monitor-Links-Post-Content.png"  width="600"/>


#### - Cron Job is started. 
- When it is executed it tests all the Posts that have not been tested.

By default it is scheduled to run every hour.
I suggest using this  **[tool](https://es.wordpress.org/plugins/wp-crontrol/)** to monitor and force Cron events.
<img src="https://staketo.wpengine.com/wp-content/uploads/2022/09/cron.png"  width="300"/>

#### - Post Links correction:
- You can edit (correct links) in each post and uncheck the box "Post checked by Cron ". This causes the Post to be retested in the next Cron event.

## Note:
This plugin runs a Cron every hour.
Uninstall the plugin to stop the Cron Event.


