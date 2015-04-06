query_builder
=============

Purpose
-------
The reason for this module is to create a sqlite query builder based on the builder design pattern.

Notes
-----

To Do
-----
- Break classes out of singular builder module.

Known Bugs
----------
- String in parameters (for WHERE clause) are not dynamically put in quotes. This causes an issue if the user doesn't quote them in the GET request