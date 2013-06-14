CSV
==

Library to easily parse CSV-Files

```
$csv = new \Rookee\Csv('data.csv');
$newHeaderNames = array(
    'name' => 'Username',
    'email' => 'Email'
);
$csv->getHeader()->convertHeader($newHeaderNames);
foreach($csv->read() as $k => $l) {
    echo $l['Username'] . ' <'.$l['Email'].'>';
}
```
