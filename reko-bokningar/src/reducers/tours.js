export default function tours(state = {}, action) {
  
  switch(action.type){

  case 'TOURS_SAVE_CATEGORIES':
    console.log(action.payload);
    console.log(state.categories);
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
    const result = originalarray.map(obj => {
      if (obj.id === newarray[0].id) {
        return newarray[0];
      } else {
        return obj;
      }
    });
    return result;
  } else {
    return newarray;
  }

}

