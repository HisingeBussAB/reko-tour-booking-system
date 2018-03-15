export default function tours(state = {}, action) {
  
  switch(action.type){

  case 'TOURS_SAVE_CATEGORIES':
    return {categories: mergeCategories(state.categories, action.payload)};

  default:
    return state;

  }
}

function mergeCategories(originalarray, newarray) {
  let arr1length;
  let arr2length;
  try { arr1length = originalarray.length; } catch(e) { arr1length = 0; }
  try { arr2length = newarray.length;      } catch(e) { arr2length = 0; }
  if (arr1length === 0) {
    return newarray;
  }

  if (arr2length === 1) {
    let result = originalarray;
    let found = false;
    found = result.find((o, i) => {
      if (o.id === newarray[0].id) {
        result[i] = newarray[0];
        return true;
      }
      return false;
    });

    if (found === false) {
      result.push(newarray[0]);
    }
    return result;
  } else {
    return newarray;
  }

}

