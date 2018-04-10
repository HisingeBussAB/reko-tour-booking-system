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
