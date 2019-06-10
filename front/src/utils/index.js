export function mergeObjectArrays (arr1, arr2, match) {
  const newarr = arr1.map(
    s => arr2.find(
      t => t[match] === s[match]) || s
  ).concat(
    arr2.filter(
      s => !arr1.find(t => t[match] === s[match])
    )
  )
  return newarr
}

export function findByKey (value, key, array) {
  for (let i = 0; i < array.length; i++) {
    if (array[i][key].toString() === value.toString()) {
      return array[i]
    }
  }
}

export function strictObjectCompare (obj1, obj2) {
  for (const p in obj1) {
    if (!looseObjectCompare(obj1[p], obj2[p])) { return false }
    for (const p2 in obj2) {
      if (typeof obj1 === 'undefined' || typeof obj1[p2] === 'undefined') { return false }
    }
  }
  return true
}

export function looseObjectCompare (obj1, obj2) {
  // Checks if porperties in object 1 are equal in object 2
  // Does not require all properties in object 2 to exist in object 1
  // Applies strict checking on subobjects
  for (const p in obj1) {
    if (typeof obj2 === 'undefined' || obj1.hasOwnProperty(p) !== obj2.hasOwnProperty(p)) { return false }
    switch (typeof obj1[p]) {
      case 'object':
        if (!strictObjectCompare(obj1[p], obj2[p])) { return false }
        break
      case 'function':
        if (typeof obj2[p] === 'undefined' || (p !== 'compare' && obj1[p].toString() !== obj2[p].toString())) { return false }
        break
      default:
        if (obj1[p] !== obj2[p]) { return false }
    }
  }
  return true
}

// https://stackoverflow.com/questions/1129216/sort-array-of-objects-by-string-property-value
export function dynamicSort (property) {
  let sortOrder = 1
  if (property[0] === '-') {
    sortOrder = -1
    property = property.substr(1)
  }
  return function (a,b) {
    /* next line works with strings and numbers, 
       * and you may want to customize it to your needs
       */
    
    const result = a[property].toString().toLowerCase() < b[property].toString().toLowerCase() ? -1 : a[property].toString().toLowerCase() > b[property].toString().toLowerCase() ? 1 : 0
    return result * sortOrder
  }
}