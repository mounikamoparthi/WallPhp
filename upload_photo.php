<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Upload a New Photo</title>
</head>
<body>
    <h1>Upload a new photo!</h1>
    <form action="proccess.php" method="post" enctype="multipart/form-data">
        <input type="file" name="fileToUpload">
        <input type="submit" value="Submit">
        <input type="hidden" name="action" value="upload_photo">
    </form>
</body>
</html>
