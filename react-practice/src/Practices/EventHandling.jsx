import react from "react";

const EventHandling = () => {
  const buttonClick = () => {
    alert("Button Clicked");
  };

  return (
    <div>
      <button onClick={buttonClick}>Click me</button>
    </div>
  );
};

export default EventHandling;
