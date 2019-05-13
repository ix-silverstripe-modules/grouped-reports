This module provides a simple tree interface for reports. It groups core reports together, and a group for SilverShop.
To add your own grouped report, create a report extending from the GroupedReport class. Then include a function called "group" that returns a string matching the name of your group.

```php

class MyReport extends GroupedReport
{
    function group()
    {
        return "Reports about the thing";
    }
```
Also included are custom Print and Export buttons. They overcome the pagination limitations of the core buttons and they do a better job of formatting output.
The export button can also accept a custom filename using `->setCustomFileName('my_file')` and it appends a timestamp and the .csv extension.