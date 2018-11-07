************
Installation
************

Manual installation
###################

In case you can't use composer (or even if you don't know it), you can manually add Punic to your project and use it. Simply download the latest version from the `Punic releases page <https://github.com/punic/punic/releases>`_.

Extract the zip archive somewhere accessible by your project and make sure you include the ``punic.php`` file found in the zip archive.


Installation using composer
###########################

As Punic is written using PSR-4, it's highly recommended to use composer to use it. Begin by editing your composer project by running.


.. code-block:: bash

    composer require "punic/punic:^3"

That's all it takes, you're ready to go! Have a look at the example on the right to see how a basic call to punic can look like.
