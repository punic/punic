# Generation of the API

1. Install composer dependencies  
   ```sh
   composer install
   ```
2. Update the API
    - On *nix systems:
      ```sh
      composer run-script update-docs
      ```
    - On Windows systems:
      ```sh
      composer run-script update-docs-win
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
