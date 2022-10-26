Please, complete following tasks:<br/>
1. Set up DB using provided mysql dump file. You may use provided docker-compose.yml if you like. But docker knowledge is not required.<br/>
2. Create a script to generate a JSON file, containing a nested `items_categories` data. Only `ID` and `name` fields are required in export.<br/>
3. Create a script to generate an Excel file, containing `items_categories` data. Only `ID` and `name` fields are required in export. Data hierarchy should be visualized.<br/>
   <br/>
*There is install.php script present, which was used to generate test-data. This script somewhat represents current state of codebase (sql.php is actually a part of a current system).*<br/>

## Run
```bash
docker-compose exec lap-stack php /var/www/html/export.php
```

Files will be created on the app/export folder