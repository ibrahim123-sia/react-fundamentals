## What Is Conditional Rendering?

Conditional rendering means **showing different UI elements** based on certain conditions (like `if` statements).

## Example 1: Using If-Else

    function UserGreeting({ isLoggedIn }) {
    if (isLoggedIn) {
        return <h2>Welcome Back </h2>;
    } else {
        return <h2>Please Log In </h2>;
    }
    }

## Example 2: Using Ternary Operator

    function Greeting({ isLoggedIn }) {
    return (
        <div>
        {isLoggedIn ? <h3>Hello, User!</h3> : <h3>Welcome, Guest!</h3>}
        </div>
    );
    }
