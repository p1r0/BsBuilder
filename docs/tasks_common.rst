Common Tasks
=====================================

Property
--------------------------------------------

The property task defines a property (kind of like a variable) that can be 
used later within the build file.

Note that all properties are *global*.

Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <property name='basename' value='my-package' />
   <property name='tarfile' value='${basename}.tar.bz2' />
   <property name='sevenzip' value='${basename}.7z' />
   <property name='target_dir' value='./build' type='prompt' />
   <property name='passw' type='password' />

Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========        
name            String          The of the property to export                   No default value        True     
value           String          The value for the property                      No default value        True
type            String          The strategy to use. View Strategies            plain                   False          
=============== =============== =============================================== ======================= =========                


Strategies
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The strategies dictate the behavior of the task.

The ones available are ``plain``, ``prompt`` and ``password``.

``plain`` just sets *value* to the property called *name*. 

``prompt`` asks the user for a value for the property *name* and if provided sets the property
to that value, otherwise sets the property to *value*.

``password`` is the same as prompt but instead of the actual text stars (``*``) are displayed.

NOTE: ``password`` works only on *nix systems.


Echo
--------------------------------------------

The echo task just prints a message to the screen.


Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <echo text='' />
   <echo text='' color='yellow' />


Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========        
text            String          The text to print                               No default value        True     
color           String          The color to use. View colors                   None                    False
=============== =============== =============================================== ======================= =========                


Colors
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The available colors to use are:

   #. black
   #. dark_gray
   #. blue
   #. light_blue
   #. green
   #. light_green
   #. cyan
   #. light_cyan
   #. red
   #. light_red
   #. purple
   #. light_purple
   #. brown
   #. yellow
   #. light_gray
   #. white


Copy
--------------------------------------------

This tasks handles the copy of one file or directory from one source to a destination.
Right now it only supports *ignoring* certain files or patterns. In the future it will support filesets.

Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <copy strategy='php' source='.' dest='./build/all' />
   <copy source='./importantscript.php' dest='./build/all' />
   <copy source='.' dest='${basedir}/all'>
         <ignore name='./test' />
         <ignore name='./build.conf.xml' />
         <ignore name='./.build_data' />
         <ignore name='./.gitignore' />
         <ignore name='./.git' />
   </copy>
   <copy source='.' dest='${basedir}/all'
         memory='on' memory_file='dist.mem'>
         <ignore name='./test' />
         <ignore name='./build.conf.xml' />
         <ignore name='./.build_data' />
         <ignore name='./.gitignore' />
         <ignore name='./.git' />
   </copy>

Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========        
source          String          The source file or directory to copy            No default value        True     
dest            String          The destination directory                       No default value        True
strategy        String          The strategy to use. View Strategies            php                     False
memory          String          Either ``on`` or ``off``                        off                     False
memory_file     String          Where to save the *memory*                      No default value        False                                        
=============== =============== =============================================== ======================= =========                


Strategies
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The strategies dictate the behavior of the task.

Right now the only one available is ``php`` which handles the copy using only PHP. In the future we will add more
and you will have the ability to create your own.

Memory
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The copy task has a special attribute named ``memory``. If you turn this ``on`` prior copying the system will do 2
things: first it will check if the ``memory_file`` exists and if yes load it, and then it will check weather each
file's m5d checksum is different from those saved in the ``memory_file`` and **only** copy those files that have
actually changed from last build.

After copying it will save the updated ``memory_file``.

This allows us to build a project, upload it, and if needed re build and upload only the difference from the latest
build.

Replace
--------------------------------------------

This tasks allows you to replace a portion of text of patter in one file.

Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <replace value='db.password=1234' 
            new_value='db.password=super_secret_password'
            file='./dist/all/configs/application.ini' />
   <replace value='db.password=1234' 
            new_value='db.password=super_secret_password'
            file='./dist/all/configs/application.ini.tpl'
            new_file='./dist/all/configs/application.ini' />

Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========        
value           String          The value to search for                         No default value        True     
new_value       String          The string to replace ``value`` with           No default value        True
file            String          The file in which to perform this operation     No default value        True
new_file        String          The filename where the replaced content will    Same value as file      False
                                be saved     
strategy        String          The strategy to use. View Strategies            simple_replace          False          
=============== =============== =============================================== ======================= =========                

NOTE: if ``file`` is not found a warning is displays but nothing happens.

Strategies
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The strategies dictate the behavior of the task.

Right now the only one available is ``simple_replace`` which uses php ``str_replace`` to do the replacing.

Package
--------------------------------------------

This tasks packages the application.

Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <package strategy='tar_bz2' name='file.tar.bz2' dest='./dist/all'  />
   <package strategy='7z' name='file.7z' dest='./dist/all'  />

Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========        
name            String          The filename for the package                    No default value        True     
dest            String          The destination directory.                      No default value        True
                                Which is the same as the source of the package  
strategy        String          The strategy to use. View Strategies            tar_bz2                 False          
=============== =============== =============================================== ======================= =========                

NOTE: if ``file`` is not found a warning is displays but nothing happens.

Strategies
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

The strategies dictate the behavior of the task.

You can user either ``tar_bz2`` or ``7z``. 

The ``tar_bz2`` strategy uses the system's tar binary to create a ``.tar.bz2`` package.

The ``7z`` strategy uses the system's 7zr binary to create a ``.7z`` package.


Version
--------------------------------------------

This task increments a version number composed of MAJOR.MINOR.BUILD in the following manner:

if type is ``build`` only the BUILD part is incremented. If type is ``major`` only the MAJOR and BUILD
parts are incremented. And finally, if type is ``minor`` only the MAJOR and MINOR parts are incremented.

You can pass an optional attribute instructing the task to export such version *number* as a property.

You also have to set a filename to save the version number.

Example
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

.. code-block:: xml

   <version type='build' file='version.txt' property='version' />

Attributes
^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^^

=============== =============== =============================================== ======================= =========        
Name            Type            Description                                     Default                 Required
=============== =============== =============================================== ======================= =========
type            Enum            One of: ``build``, ``minor``, ``major``         No default value        True             
file            String          The file to save the version num to             No default value        True     
property        String          The property name to export the version num to  No default value        false
=============== =============== =============================================== ======================= ========= 

