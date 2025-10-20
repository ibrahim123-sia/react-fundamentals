import React, { use } from "react";
import { useState, useEffect } from "react";

const Stopwatch = () => {
  const [time, setTime] = useState(0);
  const [Active, setActive] = useState(false);

  useEffect(()=>{

     let interval = null;
      if (Active) {
        interval = setInterval(() => setTime((t) => t + 1), 1000);
      }
     return () => clearInterval(interval);
  },[Active])

  return (
    <div className="flex justify-center items-center gap-10">
      <h1>Stop Watch</h1>
      <p>Time {time}</p>
      <button onClick={() => setActive(true)}>Start</button>
      <button onClick={() => setActive(false)}>Stop</button>
      <button onClick={() => setTime(0)}>Reset</button>
    </div>
  );
};

export default Stopwatch;
