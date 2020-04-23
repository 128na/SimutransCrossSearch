
a&b
[
  [type:and, cond:[a,b]]
]
-------

a&b&c
[
  [type:and, cond:[a,b,c]]
]
-------

a b&c
[
  [type:or, cond:[
    a,
    [type:and, cond:[b,c]
  ]]
]
-------

a&b c
[
  [type:or, cond:[
    [type:and, cond:[a,b]
    c,
  ]]
]
-------

a&(b c)
[
  [type:and, cond:[
    a,
    [type:or, cond:[b,c]
  ]]
]

a&(b (c d&e))
[
  [type:and, cond:[
    a,
    [type:or, cond:[
        b, 
        [type:or, cond:[c,d]
    ]]
  ]]
]
