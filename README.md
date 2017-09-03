# php-d2o-to-json
Converts Dofus d2o files to json

WIP - Should be able to:

- convert d2o->json
- convert json->d2o


Using: 

```
$d2o = new D2OReader('d2o/AbuseReasons.d2o');
$d2o->json();
```

```
$d2o = new D2OWriter('d2o/AbuseReasons.json');
$d2o->d2o();
```
