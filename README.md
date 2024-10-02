# Falla

## Description

Falla is an app that helps users choose clothes based on the current weather. Using weather data obtained from the API, the app suggests suitable clothing options for users to stay comfortable outdoors.

## Features

- Get real-time weather data via API.
- Clothing recommendations based on temperature, precipitation, wind, and other weather conditions.
- Customizable user preferences such as sensitivity to cold or heat.
- Intuitive interface for easy use.

## Authors

- [Fēliks Regzdiņš](https://github.com/21DP3FRegz)
- [Artjoms Pimanovs](https://github.com/13DPAPIMA)
- Laura Levica
Agnese Tarvida
Kristiāns Ozoliņš

## Project Structure
```
Falla/
├── backend/  - backend with Laravel
└── frontend/   - frontend with Vue
```

## Requirements
- Node.js (recommended version 14.x or higher)
- npm (usually comes with Node.js)
- Composer
- PHP (8.2 or higher)

## Installation and Running the Project

### Step 1: Clone the Repository
First, clone the repository to your local machine:
```bash
git clone https://github.com/13DPAPIMA/Falla.git
cd Falla
```

### Step 2: Setup and Run the Frontend

1. Navigate to the `frontend` folder:
    ```bash
    cd frontend
    ```

2. Install the dependencies:
    ```bash
    npm install
    ```

3. Start the development server:
    ```bash
    npm run dev
    ```

### Step 3: Setup and Run the Backend

1. Navigate to the `backend` folder:
    ```bash
    cd backend
    ```

2. Install the dependencies:
    ```bash
    composer install
    ```

3. Create a `.env` file from the `.env.example` file:
    ```bash
    cp .env.example .env
    ```

4. Generate a new application key:
    ```bash
    php artisan key:generate
    ```

5. Configure the database settings in the `.env` file.

6. Run the database migrations:
    ```bash
    php artisan migrate --seed
    ```
7. Start the local server:
    ```bash
    php artisan serve
    ```
# Instructions for obtaining an API key from OpenWeatherMap

## Step 1: Register on the OpenWeatherMap website

Go to [OpenWeatherMap](https://openweathermap.org/).

In the top right corner, click the Sign In button.

If you already have an account, enter your details and sign in. If not, click Sign Up.

Fill out the registration form with your email address, username, and password.

Verify your account by clicking the link sent to the email address you provided.

## Step 2: Obtaining an API Key

Once you are logged into your account on the OpenWeatherMap website, go to the my API keys section.

Click the Create button if the key has not been created yet.

Enter a name for your API key and click Generate.

Copy the generated API key.

## Step 3: Paste the key into the .env file

Open the project folder.

Look for the .env file. (You copied it from .env.example) 

Paste the following code, replacing <your_key_here> with your generated API key:
```bash
OPENWEATHERMAP_API_KEY=<your_key_here>
```
Save the changes to the file.

## Running the Project

Now your project is running on the local server. You can open it in a browser at http://localhost:5173.
