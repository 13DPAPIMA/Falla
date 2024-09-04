# Falla

description...

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

## Running the Project

Now your project is running on the local server. You can open it in a browser at http://localhost:5173.
