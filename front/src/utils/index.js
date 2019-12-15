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
  try {
    for (let i = 0; i < array.length; i++) {
      if (typeof array[i] === 'object' && array[i][key].toString() === value.toString()) {
        return array[i]
      }
    }
    return undefined
  } catch (e) {
    return undefined
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
  try {
    if (property[0] === '-') {
      sortOrder = -1
      property = property.substr(1)
    }
    return function (a, b) {
      if (sortOrder === -1) {
        return b[property].localeCompare(a[property])
      } else {
        return a[property].localeCompare(b[property])
      }
    }
  } catch (e) {
    return 0
  }
}

export function getActivePlusSelectedCategories (categories, selectedItemThatHasCategories) {
  const allactivecategories = categories.filter(category => !category.isdisabled)
  const activecategoriesandselected = typeof selectedItemThatHasCategories !== 'undefined' && typeof selectedItemThatHasCategories.categories === 'object'
    ? allactivecategories.concat(selectedItemThatHasCategories.categories) : allactivecategories
  const activecategories = []
  if (typeof activecategoriesandselected === 'object') {
    const map = new Map()
    for (const item of activecategoriesandselected) {
      if (!map.has(item.id)) {
        map.set(item.id, true)
        activecategories.push({
          id   : item.id,
          label: item.label
        })
      }
    }
  }
  return activecategories
}

export function getActivePlusSelectedTours (tours, selectedTour, extraTour) {
  const allActiveTours = tours.filter(tours => !tours.isdisabled)
  if (typeof selectedTour === 'object' && selectedTour.length > 0 && typeof selectedTour[0].id !== 'undefined') {
    if (typeof findByKey(selectedTour[0].id, 'id', allActiveTours) === 'undefined') {
      allActiveTours.push(selectedTour[0])
    }
  }
  if (typeof extraTour === 'object' && extraTour.length > 0 && typeof extraTour[0].id !== 'undefined') {
    if (typeof findByKey(extraTour[0].id, 'id', allActiveTours) === 'undefined') {
      allActiveTours.push(extraTour[0])
    }
  }
  return allActiveTours
}

export const sumBy = (items, prop) => items.reduce((a, b) => +a + +b[prop], 0)
