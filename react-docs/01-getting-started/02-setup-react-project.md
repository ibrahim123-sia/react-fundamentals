# Setting Up a React Project

There are two popular ways to create a React project:  
1. Using **Create React App (CRA)** — beginner-friendly but heavier (not recommended by react team). 
CRA comes with a significant number of pre-installed dependencies and build tools, including Webpack, Babel, ESLint, and various development utilities.
2. Using **Vite** — modern, faster, and recommended.


## Option 1: Using Create React App

Step 1: Install Node.js
    Download and install Node.js from [https://nodejs.org]. 
        You can check if it’s installed using:
        ```bash
        node -v
        npm -v

Step 2: Create React App
    Run this in your terminal:
        npx create-react-app my-first-react-app

Step 3: Start the Development Server
    cd my-first-react-app
        npm start


## Option 2: Using Create React App

Step 1: Create a New Project
    npm create vite@latest my-react-app

    Select:
        Select a framework: › React
        Select a variant: › JavaScript

Step 2: Install Dependencies
    cd my-react-app
        npm install

Step 3: Run the Project
    npm run dev
    Visit the URL shown [like http://localhost:5173/] — your app is live.