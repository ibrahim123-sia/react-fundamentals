## What Are Events?

Events are actions performed by the user — like clicks, typing, or hovering.

React uses **camelCase event names** and **function references**.

    Handling Events:

        Example:
        function ClickExample() {
        function handleClick() {
            alert("Button clicked!");
        }

        return <button onClick={handleClick}>Click Me</button>;
        }

    Key Points:
    Use onClick, onChange, onSubmit, etc.
    Do not call the function directly → onClick={handleClick} not onClick={handleClick()}

    Using Arrow Functions
    You can also define inline functions:
    <button onClick={() => alert("Clicked!")}>Click</button>

    Handling Input Change
    import { useState } from "react";

    function InputExample() {
    const [text, setText] = useState("");

    function handleChange(e) {
        setText(e.target.value);
    }

    return (
        <div>
        <input type="text" value={text} onChange={handleChange} />
        <p>You typed: {text}</p>
        </div>
    );
    }

Common Events
Event Description
onClick User clicks element
onChange Input value changes
onSubmit Form submission
onMouseEnter Mouse hovers
onKeyDown Key press
