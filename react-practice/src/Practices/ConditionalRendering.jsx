import React, { useState } from 'react'

const ConditionalRendering = () => {

    const [login,setLogin] = useState(false)

  return (
    <div>
        {/* conditional rendering by ternary operator */}
        <p>{!login? 'Login Your Account':'Welcome back'}</p> 
        <button onClick={()=>setLogin(true)}>Click me to logon </button>
    </div>
  )
}

export default ConditionalRendering
