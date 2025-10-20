import React from 'react'
import { useState, useEffect } from "react";

const Stopwatch = () => {
    const [time, setTime] = useState(0);
    const [running, setRunning] = useState(false);

    useEffect(()=>{
        let interval=null;
        if(running){
            interval = setInterval(() => setTime(t => t + 1), 1000);
        }
        return () => clearInterval(interval);
    },[running])

  return (
   <div className='flex justify-center items-center gap-10 '>
      <h2 className='text-black'>⏱ Time: {time}s</h2>
      <button onClick={() => setRunning(true)}>Start</button>
      <button onClick={() => setRunning(false)}>Stop</button>
      <button onClick={() => setTime(0)}>Reset</button>
    </div>
  )
}

export default Stopwatch
