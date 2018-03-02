
export function toggleMenuCompression(compressed) {
  return function(dispatch){
    dispatch({
      type: 'CHANGE_STYLES',
      payload: {mainmenu: {compressed: compressed}}
    });
    

    
  };
}