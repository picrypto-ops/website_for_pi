document.addEventListener("DOMContentLoaded", () => {
  const canvas = document.createElement("canvas")
  const ctx = canvas.getContext("2d")
  const container = document.getElementById("pi-bg-wave")

  container.appendChild(canvas)

  let width, height, wave

  function resize() {
    width = container.clientWidth
    height = container.clientHeight
    canvas.width = width
    canvas.height = height

    wave = new Wave(ctx, width, height)
  }

  class Wave {
    constructor(ctx, width, height) {
      this.ctx = ctx
      this.width = width
      this.height = height
      this.color = "#0033a0"
    }

    draw(time) {
      const { ctx, width, height, color } = this

      ctx.beginPath()
      ctx.moveTo(0, height)

      for (let x = 0; x < width; x++) {
        const y = Math.sin(x * 0.01 + time * 0.1) * 20 + height * 0.7
        ctx.lineTo(x, y)
      }

      ctx.lineTo(width, height)
      ctx.fillStyle = color
      ctx.fill()
    }
  }

  function animate(time) {
    ctx.clearRect(0, 0, width, height)
    wave.draw(time)
    requestAnimationFrame(animate)
  }

  window.addEventListener("resize", resize)
  resize()
  animate(0)
})

