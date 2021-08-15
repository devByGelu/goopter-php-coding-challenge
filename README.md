# goopter-php-coding-challenge
**ABOUT THE PROJECT**

The following google drive contains a google sheet file which contains a list of product name and image file name.
 https://drive.google.com/drive/folders/1Jde0kra1hzIH75sViQd4BtuON9PoxUKS?usp=sharing


The following google imagdrive contains image files of the items, the image name is close to the product name, but can be slightly different. E.g.  the product: Ebi Tempura (5 pcs) has a matching picture file:  Ebi Tempura.JPG; product: Bluefin Chu Toro has a matching picture file: ​​BluefinChutoro.jpg; product: Spicy Fried Gyoza (5 pcs) has a matching picture file: Spicy Fried Gyoza (5 pieces).jpg etc.

https://drive.google.com/drive/folders/11PJEUZl8QmZlNSl23_AfF3cjxKa-wgJH?usp=sharing


**Requirement:**
Build a command line php script, to loop through the source file and image source folders(including sub-folders), find the closest matching image for each product, copy over the image to the image-output-{your-name}-{date}  folder; rename it to lowercase, and replace the space in the image file name with “-”, remove the non letter and non number characters in the file name, put the final image name into the google sheet. E.g. ebi-tempura.jpg

Requirements: 
Add good comments in the code;
Follow coding standard, provide clean and easy to read code;
Add enough cases to support different cases, make the code be smart;
Keep track of your time and report the total hours you spent.
Upload source code under php-code-{your-name}-{date} folder


API reference:


https://developers.google.com/drive/api/v3/quickstart/php



**SETTING UP**
1. Install packages composer install
2. Enable Drive + Sheets API from Google Cloud Console
3. Make sure php-json is enabled
4. Accomplish config.json
5. Run php index.php

**config.json**
|             CONFIG            |                                                               DESCRIPTION                                                               |
|:-----------------------------:|:---------------------------------------------------------------------------------------------------------------------------------------:|
| credentials_path              | path to your credentials.json (can be downloaded from Cloud console).                                                                   |
| credentials_path              | path token.json is saved (created automatically).                                                                                       |
| source_file_id                | sheet id of the source file.                                                                                                            |
| source_fle_target_sheet_names | If [ ], will consider all the sheets in the source file. else, will consider the sheet names inside the array (eg: ['Sheet1','Sheet2']. |
| source_file_name_column       | Where the column of the product names reside.                                                                                           |
| source_file_read_start        | Row # of the first product entry.                                                                                                       |
| images_root_folder_id         | folder id where the subfolders containing the images reside.                                                                            |
| similarity_tolerance          | If percentage result of in-built function similar_text is less than this entry, the product will not be associated with an image.       |
| output_folder_id              | Where the associated images of the products are copied to.                                                                              |

![image](https://user-images.githubusercontent.com/41291228/129488157-fb09ce4e-64b6-4280-b094-d4d63aa75d91.png)
