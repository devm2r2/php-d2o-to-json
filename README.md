# php-d2o-to-json (2.42 compatible)
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


# Example of json output

```
{
  "def": [
    {
      "memberName": "Job",
      "packageName": "com.ankamagames.dofus.datacenter.jobs",
      "fields": [
        {
          "name": "id",
          "type": -1,
          "vectorTypes": false
        },
        {
          "name": "nameId",
          "type": -5,
          "vectorTypes": false
        },
        {
          "name": "specializationOfId",
          "type": -1,
          "vectorTypes": false
        },
        {
          "name": "iconId",
          "type": -1,
          "vectorTypes": false
        },
        {
          "name": "toolIds",
          "type": -99,
          "vectorTypes": [
            {
              "name": "Vector.",
              "type": -1
            }
          ]
        }
      ]
    }
  ],
  "data": [
    {
      "id": 1,
      "nameId": 2736,
      "specializationOfId": 0,
      "iconId": -1,
      "toolIds": []
    },
    {
      "id": 2,
      "nameId": 2706,
      "specializationOfId": 0,
      "iconId": 1,
      "toolIds": [
        675,
        515,
        674,
        676,
        502,
        8539,
        782,
        673,
        923,
        771,
        456,
        454,
        478,
        927
      ]
    },
    {
      "id": 11,
      "nameId": 2737,
      "specializationOfId": 0,
      "iconId": 10,
      "toolIds": [
        494
      ]
    }
    /*  Etc... */
  ]
}
```
