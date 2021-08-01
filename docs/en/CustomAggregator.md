# Custom Aggregator

A custom aggregator interface will allow a model to provide its own datalist of switchflittables.

```php
<?php

use SwitchFlit\SwitchFlitable;
use SwitchFlit\SwitchFlitAggregator;

class MyDataObject extends DataObject implements SwitchFlitable, SwitchFlitAggregator
{
    private static $db = [
        'Name' => 'Varchar(100)',
        'Flitable' => 'Boolean(0)'
    ];
    
    public function SwitchFlitTitle()
    {
        return $this->Name;
    }
    
    public function SwitchFlitLink()
    {
        return '/mydataobjects/' . $this->ID;
    }
    
    public static function SwitchFlitArrayList()
    {
        // Be warned that there is an instanceof check for arraylist.
        return new ArrayList(
            self::get()->filter([
                'Flitable' => true
            ])
        );
    }
}
```

Now with this example `MyDataObject` is both an Aggregator of `SwitchFlitables` and a `SwitchFlitable` itself.
While using a switchflit switcher that referers to this dataobject all instance of `MyDataObject` will be switchable.

Lets move onto a more interesting example:

_App/src/AnimalHouse/Dog.php_
```php
<?php

use SwitchFlit\SwitchFlitable;

class Dog extends Animal implements SwitchFlitable
{
    private static $db = [
        'Nickname' => 'Varchar(100)',
    ];
    
    public function SwitchFlitTitle()
    {
        return $this->Nickname;
    }
    
    public function SwitchFlitLink()
    {
        return '/animals/dogs/' . $this->ID;
    }
}
```

_App/src/AnimalHouse/Cat.php_
```php
<?php

use SwitchFlit\SwitchFlitable;

class Cat extends Animal implements SwitchFlitable
{
    private static $db = [
        'Name' => 'Varchar(100)',
    ];
    
    public function SwitchFlitTitle()
    {
        return $this->Name;
    }
    
    public function SwitchFlitLink()
    {
        return '/animals/cats/' . $this->ID;
    }
}
```

_App/src/AnimalHouse/Animal.php_
```php
<?php

use SwitchFlit\SwitchFlitable;

class Animal extends DataObject implements SwitchFlitAggregator
{
    private static $db = [
        'Age' => 'Int'
    ];

    public static function SwitchFlitArrayList() {
    {
        // You should probably use relationallists here but you get the point.
        $SwitchFlitableAnimals = (new ArrayList(Dogs::get()))->merge(Cats::get())
        return $SwitchFlitableAnimals;
    }
}
```

Now when using a switcher which references `Animal` results will include both dogs n cats n dogs n cats n dogs n cats. (Sorry.)
There are two drawbacks to using a CustomAggregator:
    - Much slower. Multiple checks are preformed on every record.
    - Every Dataobject you include using `SwitchFlitAggregator::SwitchFlitArrayList`'s arraylist still needs to implement `SwitchFlitable`

