# distance #

Provide a standard way to store distance and work with it.

# Usage #

Initialising the classes.
 
```

use Drupal\distance\Distance;

$distance = new Distance(1.67, 'MI');
$km = $distance->toKilometers();

```

Using the utility class.

```

use Drupal\distance\Utility\CoordinateCalculator;

$distance = DistanceCalculator::calculateDistance($coordinate_1, $coordinate_2);
$distances = DistanceCalculator::calculateCollectionDistance($coordinate_collection);

```

# Release notes #

`1.0.0`
* Basic setup of the module.
* Provide distance class.
