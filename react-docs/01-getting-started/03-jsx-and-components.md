## What is JSX?

**JSX** stands for **JavaScript XML**.  
It allows us to write HTML-like syntax directly inside JavaScript.  
This makes React code easier to read and helps visualize the UI structure.

Example:
const element = <h1>Hello, React!</h1>;

    Under the hood, React converts this into:
    const element = React.createElement("h1", null, "Hello, React!");

## Rules of JSX
    1.Must return one root element
    // Correct
    return (
    <div>
        <h1>Hello</h1>
        <p>Welcome!</p>
    </div>
    );

    // Wrong
    return (
    <h1>Hello</h1>
    <p>Welcome!</p>
    );

    2.Use className instead of class
        <h1 className="title">Welcome</h1>

    3.Use camelCase for event names
        <button onClick={handleClick}>Click Me</button>

    4.Use curly braces {} for JavaScript expressions
        const name = "Ibrahim";
        <h2>Hello, {name}</h2>

## What are Components?

    A component is a reusable piece of UI.
    React apps are built by combining multiple small components.

    There are two main types:

    Functional Components (modern & preferred)

    Class Components (older)

    Functional Component Example:
        function Greeting() {
        return <h2>Hello from Greeting Component</h2>;
        }

        export default Greeting;

    To use it inside another component (like App.jsx):
        import Greeting from "./Greeting";

        function App() {
        return (
            <div>
            <Greeting />
            <p>This is my first React component!</p>
            </div>
        );
        }

        export default App;


    Component Naming Rules

        Component names must start with a capital letter (App, not app)

        Must return JSX

        Always export your component (default or named export)

    Why Components?

        Reusable – build once, use anywhere
        Organized – separate code into small parts
        Scalable – easy to maintain large projects    