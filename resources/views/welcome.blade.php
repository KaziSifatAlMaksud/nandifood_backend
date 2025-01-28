<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600&family=Roboto:wght@400&display=swap" rel="stylesheet">

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to NandiFoods</title>
    <style>
        body {
            margin: 0;
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f5f6f7;
            color: #333;
        }

        .container {
            text-align: center;
            padding: 20px;
            border: 2px solid #ddd;
            border-radius: 10px;
            background: white;
            transition: transform 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
        }

        .container:hover {
            transform: translateY(-10px); /* Moves the card up slightly */
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.2); /* Adds a more prominent shadow */
        }

        .logo {
            max-width: 150px;
            margin-bottom: 20px;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 600;
            font-family: 'Roboto', sans-serif;
            color: #333;
        }

        .message {
            font-size: 1.2rem;
            font-family: 'Roboto', sans-serif;
            color: #555;
        }
    </style>
</head>
<body>
    <div class="container">
        <img src="https://nanidifood.tor1.digitaloceanspaces.com/logo-horizontal.png" alt="Company Logo" class="logo">
        <h1 class="title">Welcome to NandiFoods</h1>
        <p class="message">Server is Successfully Connected</p>
    </div>
</body>
</html>
