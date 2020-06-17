### Goals API

API built  for mobile app [Goals - Android](https://github.com/NarminaS/Goals-Android "Goals - Android")

- Supports JTW authorization;
- "ASP.NET" attribute like model validation 

#### My custom framework  example of model validation decoration
```php
<?php

class AuthModel extends ModelBase 
{
    public $Email = ['Required', 'Email'];
    public $Password = ['Required'];

    function __construct()
    {
        parent::__construct();
    }
}

```

#### Real ASP.NET model validation example 

```csharp
    public class AuthModel
    {
        [Required]
	[DataType(DataType.EmailAddress)]
        public string Email { get; set; }

        [Required]
        public string Password { get; set; }
    }
``` 

- 100% custom light "ASP.NET MVC"like MVC framework;

### Coming soon
- [ ] Views support
- [ ] Multiple authorization types support (not only JWT)
