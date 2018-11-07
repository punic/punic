# Generation of the API

This is done automatically by a TravisCI job.

If you want to do it locally:

1. Install composer dependencies  
   ```sh
   composer install
   ```
2. Update the API
   ```sh
   composer run-script update-docs
   ```


# Generation of the documentation

This is done automatically by readthedocs.org.

If you want to do it locally:

- install Python
- install sphinx
  ```sh
  pip install sphinx
  ```
- generate the documentation
  ```sh
  make html
  ```
