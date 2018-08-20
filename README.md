# Date Interval #

Provide a standard way to store a date interval in Drupal.

# Usage #

Initialising the classes.
 
```

use Drupal\date_interval\DrupalDateInterval;

$date_interval = DrupalDateInterval::createFromIntervalSpec('P2D');
$interval = $date_interval->format('%d', TRUE, ' ', TRUE);

```

# Release notes #

`8.x-1.1`
+ Basic setup of the module.
+ Provide a DateIntervalPlus and a DrupalDateInterval class.
+ Provide unit tests for DateIntervalPlus and DrupalDateInterval.
+ Improve flexibility and unit mapping of format() function.
